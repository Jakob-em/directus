import { GraphQLBoolean, GraphQLFloat, GraphQLInt, GraphQLString } from 'graphql';
import { GraphQLJSON } from 'graphql-compose';
import { types } from '../types';

export function getGraphQLType(localType: typeof types[number] | 'alias' | 'unknown') {
	switch (localType) {
		case 'boolean':
			return GraphQLBoolean;
		case 'bigInteger':
		case 'integer':
			return GraphQLInt;
		case 'decimal':
		case 'float':
			return GraphQLFloat;
		case 'csv':
		case 'json':
			return GraphQLJSON;
		default:
			return GraphQLString;
	}
}
