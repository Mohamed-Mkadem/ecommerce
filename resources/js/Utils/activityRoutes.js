export function getActivitySubjectRoute(subjectType, subjectId) {
    switch (subjectType) {
        case "App\\Models\\Order":
            return { name: "orders.show", params: { order: subjectId } };
        case "App\\Models\\Product":
            return {
                name: "products.show",
                params: { product: subjectId },
            };
        case "App\\Models\\User":
            return {
                name: "employees.show",
                params: { employee: subjectId },
            };
        case "App\\Models\\Client":
            return {
                name: "clients.show",
                params: { client: subjectId },
            };
        default:
            return null;
    }
}
