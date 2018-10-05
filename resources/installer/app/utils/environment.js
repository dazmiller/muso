
export function generateEnvContent(env) {
  return Object.keys(env).map((key) => {
    // Don't add quotes to boolean values
    if (env[key] === true || env[key] === false || key === 'APP_KEY' || key === 'JWT_FACEBOOK_SECRET') {
      return `${key}=${env[key]}`;
    }

    // Ignore `JWT_SECRET` as it will be added by the server
    if (key === 'JWT_SECRET') {
      return `${key}=`;
    }

    return `${key}="${env[key]}"`;
  }).join('\n');
}
