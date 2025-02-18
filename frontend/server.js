import { createRequestHandler } from '@react-router/node';
import { installGlobals } from '@remix-run/node';
import * as build from './build/server/index.js';

installGlobals();

export default createRequestHandler(build);