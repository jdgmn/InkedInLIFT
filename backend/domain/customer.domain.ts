export interface CustomerDbDto {
    ID: number;
    FName: string;
    LName: string;
    ContactNo: string;
}

export interface CustomerDto {
    id: number;
    fullName: string;
    contactNumber: string;
}

export function toCustomerDto(data: CustomerDbDto): CustomerDto {
    return {
        id: data.ID,
        fullName: `${data.FName} ${data.LName}`,
        contactNumber: data.ContactNo,
    };
}