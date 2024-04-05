export const getRequestError = (error: any) => {
  if (!error?.response?.data) return error.message;

  const errorKeys = Object.keys(error.response.data);

  if (errorKeys.length) return error.response.data[errorKeys[0]];

  return error.message;
};

export const getRequestErrorArray = (error: any) => {
  if (!error?.response?.data || error.response.status === 500)
    return error.message;

  const errorKeys = Object.keys(error?.response?.data[0]);

  if (errorKeys.length) return error.response.data[0][errorKeys[0]];

  return error.message;
};
