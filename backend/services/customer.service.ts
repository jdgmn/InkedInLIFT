import connection from "../connection/db.connect.ts";
import { CustomerDbDto, toCustomerDto } from "../domain/customer.domain.ts";

export async function fetchCustomers() {
    const [rows] = await connection.execute("SELECT * FROM customers");
    const customers = (rows as CustomerDbDto[]).map(toCustomerDto); // Transform data
    return customers;
}