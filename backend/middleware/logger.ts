import { MiddlewareHandler } from "@hono/hono";

export const logger: MiddlewareHandler = async (c, next) => {
    console.log(`${c.req.method} ${c.req.url}`);
    await next();
};