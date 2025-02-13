import registerComponent from '@/utils/register-component/';
import { getInterfaces } from './index';
import { Component } from 'vue';
import api from '@/api';
import { getRootPath } from '@/utils/get-root-path';
import asyncPool from 'tiny-async-pool';

const interfaces = getInterfaces();

export async function registerInterfaces() {
	const context = require.context('.', true, /^.*index\.ts$/);

	const modules = context
		.keys()
		.map((key) => context(key))
		.map((mod) => mod.default)
		.filter((m) => m);

	try {
		const customResponse = await api.get('/extensions/interfaces');
		const interfaces: string[] = customResponse.data.data || [];

		await asyncPool(5, interfaces, async (interfaceName) => {
			try {
				const result = await import(
					/* webpackIgnore: true */ getRootPath() + `extensions/interfaces/${interfaceName}/index.js`
				);
				modules.push(result.value.default);
			} catch (err) {
				console.warn(`Couldn't load custom interface "${interfaceName}"`);
			}
		});
	} catch {
		console.warn(`Couldn't load custom interfaces`);
	}

	interfaces.value = modules;

	interfaces.value.forEach((inter) => {
		registerComponent('interface-' + inter.id, inter.component);

		if (typeof inter.options !== 'function' && Array.isArray(inter.options) === false) {
			registerComponent(`interface-options-${inter.id}`, inter.options as Component);
		}
	});
}
