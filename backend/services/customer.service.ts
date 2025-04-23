import connection from "../connection/db.connect.ts";

export async function fetchCustomers() {
    const [rows] = await connection.execute("SELECT * FROM customers");
    return rows;
}