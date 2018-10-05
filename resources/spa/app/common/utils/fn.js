
/**
 * An empty function to use as default value
 * for function variables
 */
export function noop() { }

/**
 * Calls a function after the delay, if the function is called
 * before the delay, then it resets the delay to 0.
 * Usefull to prevent calling the function really quick, and only
 * once at the end.
 * Best use case: Autocomplete calls to API
 * @param {Function} fn The function to call after the delay
 * @param {Number} delay The milliseconds to delay
 */
export function debounce(fn = noop, delay = 300) {
  let timer = null;

  return function () {
    const args = arguments;

    if (timer) {
      clearTimeout(timer);
    }

    timer = setTimeout(() => {
      fn.apply(this, args);
    }, delay);
  };
}
