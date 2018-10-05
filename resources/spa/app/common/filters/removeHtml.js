let RemoveHtml = () => {

    return function (value, wordwise, max, tail) {
        value = String(value).replace('&nbsp;',' ');

        return value ? String(value).replace(/<[^>]+>/gm, ' ') : '';
    };
};

export default RemoveHtml;