export const privateRoutes = ["/dashboard", "/dashboard/profile"];

export const validateAuthPath = (pathname: string, authorized: boolean) => {
    if (!authorized && privateRoutes.includes(pathname)) {
        return true;
    }
    return false;
};
