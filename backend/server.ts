import "https://deno.land/std@0.192.0/dotenv/load.ts";
import { Hono } from "@hono/hono";
import {setupRoutes} from "./routes/setup_routes.ts";
import { logger } from "./middleware/logger.ts";

const app = new Hono();

app.use("*", logger);
setupRoutes(app);

Deno.serve(app.fetch);
console.log("Server running on http://localhost:8000");