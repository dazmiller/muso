/**
 * Returns the title for the main page
 */
let MainTitle = () => {
  return function (value, wordwise, max, tail) {
    return `${value} - ${Configurations.APP_TITLE}`;
  };
};

export default MainTitle;
