
/**
 * Generates an API format date from a Date object
 * @param {Date} date Date to format
 */
export function toApiDate(date) {
  return date.toISOString().slice(0, 19).replace('T', ' ');
}
