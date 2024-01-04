export const getResetPasswordParams = () => {
    let currentUrl = window.location.href;

    let url = new URL(currentUrl);

    let searchParams = url.searchParams;

    let token = searchParams.get("token");
    let email = searchParams.get("email");

    return { token, email };
};
