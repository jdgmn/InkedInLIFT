import { Hono } from "@hono/hono";
import { getCustomers } from "../controllers/customer_controller.ts";



export const setupRoutes = (app: Hono) => {
    app.get("/", (c) => c.text("Welcome to the API"));
    app.get("/customers", getCustomers);
};