import mysql from "npm:mysql2@^2.3.3/promise";

const connection = await mysql.createConnection({
    host: Deno.env.get("DB_HOST") || "localhost",
    user: Deno.env.get("DB_USER") || "root",
    password: Deno.env.get("DB_PASSWORD") || "",
    database: Deno.env.get("DB_NAME") || "lift_db"
});
export default connection