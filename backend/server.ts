import "https://deno.land/std@0.192.0/dotenv/load.ts";
import { Hono } from "@hono/hono";
import { customerRoutes } from "./routes/customer.routes.ts";
import { logger } from "./middleware/logger.ts";
import { cors } from "jsr:@hono/hono/cors";


const app = new Hono();

app.use("*", cors());
app.use("*", logger);

customerRoutes(app);

Deno.serve(app.fetch);
console.log("Server running on http://localhost:8000");