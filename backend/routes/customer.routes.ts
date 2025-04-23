import { Hono } from "@hono/hono";
import { getCustomers } from "../controllers/customer.controller.ts";


export const customerRoutes = (app: Hono): void => {
    app.get("/api/customers", getCustomers);
};