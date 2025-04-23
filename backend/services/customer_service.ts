import connection from "../connection/db_connect.ts";

export async function fetchCustomers() {
    const [rows] = await connection.execute("SELECT * FROM customers");
    return rows;
}