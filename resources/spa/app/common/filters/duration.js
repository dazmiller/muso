
/**
 * Receives a time string value in `00:00:00` format and returns only seconds
 * and minutes if hours are not pressent.
 */
let Duration = () => {

  return function (value) {
    if (value) {
      if (value[0] === '0' && value[1] === '0') {
        return value.substring(3);
      }
    }

    return value;
  };
};

export default Duration;