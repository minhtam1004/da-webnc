// Views
import Home from "./components/Home";
import Profile from "./components/Profile";
import Login from "./components/Login";
import RegisterCustomer from "./components/RegisterCustomer";
import AuthContainer from "./components/AuthContainer";
import Container from "./components/Container";

import Dashboard from "./components/Dashboard";
import Tables from "./components/Transaction/Receiving";
import Maps from "./components/Maps";
import BadGateway from "./components/BadGateway";
import AccountInfo from "./components/AccountInfo";
import Transaction from "./components/Transaction";
import ListEmployee from "./components/ListEmployee";
import ListCustomer from "./components/ListCustomer";
import CustomerDetail from "./components/Customers/CustomerDetail";
import TransferInterbank from "./components/TransferInterbank";
import EmployeeDetail from "./components/Employee/EmployeeDetail";
import Topup from "./components/Topup";
import ListBank from "./components/ListBank";
import DebtReminder from "./components/DebtReminder";
import BankDetail from "./components/InterBank/BankDetail";
import CreateDebt from "./components/CreateDebt";
export const routes = [
    { path: "/login", name: "Login", component: Login },
    { path: "/customer/:id", name: "CustomerDetail", component: CustomerDetail },
    { path: "/employee/:id", name: "EmployeeDetail", component: EmployeeDetail },
    { path: "/banks/:id", name: "BankDetail", component: BankDetail },
    {
        path: "",
        component: Container,
        children: [
            {
                path: "/",
                name: "Dashboard",
                component: Dashboard,
                props: { page: 1 },
                alias: "/dashboard",
                meta: { role: 3 }
              
            },
            {
                path: "/profile",
                name: "Profile",
                props: { page: 2 },
                component: Profile
            },
            {
                path: "/account-info",
                name: "AccountInfo",
                props: { page: 3 },
                component: AccountInfo
            },
            {
                path: "/transfers",
                name: "Topup",
                props: { page: 4 },
                component: Topup
            },
            {
                path: "/transfers-interbank",
                name: "TransferInterbank",
                props: { page: 5 },
                component: TransferInterbank
            },
            {
                path: "/add-debt-reminder",
                name: "CreateDebt",
                props: { page: 6 },
                component: CreateDebt
            },
            {
                path: "/employees",
                name: "ListEmployee",
                props: { page: 7 },
                component: ListEmployee
                
            },
            {
                path: "/customers",
                name: "ListCustomer",
                props: { page: 8 },
                component: ListCustomer
                
            },
            {
                path: "/transactions",
                name: "Transactions",
                props: { page: 9 },
                component: Transaction
            },

            // {
            //     path: "/maps",
            //     name: "Maps",
            //     props: { page: 6 },
            //     component: Maps
            // },
            {
                path: "/banks",
                name: "ListBank",
                props: { page: 10 },
                component: ListBank
            },
            {
                path: "/register-customer",
                name: "RegisterCustomer",
                props: { page: 11 },
                component: RegisterCustomer
            },
            {
                path: "/debt-remider",
                name: "DebtReminder",
                props: { page: 12 },
                component: DebtReminder
            },
            {
                path: "/404",
                name: "BadGateway",
                props: { page: 13 },
                component: Tables
            },
            {
                path: "*",
                props: { page: 14 },
                redirect: "/404"
            }
        ]
    }
];
