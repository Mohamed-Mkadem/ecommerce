export default [
    {
        icon: `<i class="ri-dashboard-horizontal-fill"></i>`,
        label: "Dashboard",
        route: route("dashboard"),
        component: "Dashboard",
    },
    {
        icon: `<i class="ri-shopping-cart-fill"></i>`,
        label: "Orders",
        route: "#",
        component: "Orders",
        children: [
            {
                label: "New Order",
                route: route("orders.create"),
                component: "Admin/Orders/Create",
            },
            {
                label: "Orders",
                route: route("orders.index"),
                component: "Admin/Orders/Index",
            },
            {
                label: "Actions",
                route: route("ordersActions"),
                component: "Admin/Orders/Actions",
            },
            {
                label: "Shipping Reports",
                route: route("shipping_reports.index"),
                component: "Admin/Shipping_Reports/Index",
            },
            {
                label: "NRP",
                route: route("nrp.index"),
                component: "Admin/NRPs/Index",
            },
        ],
    },
    {
        icon: `<i class="ri-cake-3-line"></i>`,
        label: "Products",
        route: "#",
        component: "Products",
        children: [
            {
                label: "New Product",
                route: route("products.create"),
                component: "Admin/Products/Create",
            },
            {
                label: "Products",
                route: route("products.index"),
                component: "Admin/Products/Index",
            },
            {
                label: "Wrappers",
                route: route("wrappers.index"),
                component: "Admin/Wrappers/Index",
            },
            {
                label: "New Wrapper",
                route: route("wrappers.create"),
                component: "Admin/Wrappers/Create",
            },
        ],
    },
    {
        icon: `<i class="ri-id-card-line"></i>`,
        label: "Employees",
        route: route("employees.index"),
        component: "Employees/Index",
        onlyAdmin: true,
    },
    {
        icon: `<i class="ri-group-line"></i>`,
        label: "Clients",
        route: route("clients.index"),
        component: "Clients/Index",
    },
    {
        icon: `<i class="ri-star-line"></i>`,
        label: "Reviews",
        route: route("reviews.index"),
        component: "Reviews/Index",
    },
    {
        onlyAdmin: true,
        icon: `<i class="ri-money-dollar-circle-fill"></i>`,
        label: "Invoices",
        route: route("invoices.index"),
        component: "Invoices/Index",
    },
    {
        onlyAdmin: true,
        icon: `<i class="ri-advertisement-line"></i>`,
        label: "Coupon Codes",
        route: route("coupons.index"),
        component: "Coupon Codes/Index",
    },

    {
        icon: `<i class="ri-notification-line"></i>`,
        label: "Notifications",
        route: route("notifications.index"),
        component: "Notifications",
    },
    {
        icon: `<i class="ri-bar-chart-line"></i>`,
        label: "Statistics",
        route: "#",
        component: "Statistics",
        onlyAdmin: true,
        children: [
            {
                label: "Earnings",
                route: route("stats.earnings"),
                component: "Admin/Statistics/Earnings",
            },
            {
                label: "Orders",
                route: route("stats.orders"),
                component: "Admin/Statistics/Orders",
            },
            {
                label: "Products",
                route: route("stats.products"),
                component: "Admin/Statistics/Products",
            },
            {
                label: "Offers",
                route: route("stats.packs"),
                component: "Admin/Statistics/Packs",
            },
            {
                label: "Coupon Codes",
                route: route("stats.couponCodes"),
                component: "Admin/Statistics/CouponCodes",
            },
            {
                label: "Clients",
                route: route("stats.clients"),
                component: "Admin/Statistics/Clients",
            },
            {
                label: "Reviews",
                route: route("stats.reviews"),
                component: "Admin/Statistics/Reviews",
            },
        ],
    },
    {
        icon: `<i class="ri-settings-3-line"></i>`,
        label: "Settings",
        route: "#",
        component: "Admin/Settings/Index",
        onlyAdmin: true,
        children: [
            {
                label: "Shippers",
                route: route("shippers.index"),
                component: "Admin/Shippers/Index",
            },
            {
                label: "Shipping",
                route: route("states.index"),
                component: "Admin/States/Index",
            },
        ],
    },
];
