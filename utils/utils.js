function fetchJsToObj (soure, callback) {
    fetch(soure, { method: 'GET' })
        .then(response => response.text())
        .then(data => {
            let newVariable = data.replace(/(?:var|let|const)\s(\w+)\s=/g, '"$1":').replace(/;/g, ',').replace(/,$/g, '').replace(/'/g, '"');
            return JSON.parse('{' + newVariable + '}');
        }).then(DataObj => {
            callback(DataObj);
        });
}
function findHighestZIndex (elem) {
    const elems = document.getElementsByTagName(elem);
    let highest = 0;
    for (let i = 0; i < elems.length; i++) {
        const zindex = document.defaultView.getComputedStyle(elems[i], null).getPropertyValue('z-index');
        if ((zindex > highest) && (zindex !== 'auto')) {
            highest = zindex;
        }
    }
    return parseInt(highest, 10);
}

function getDomPosition (elem, property) {
    if (property === 'top') return elem.getBoundingClientRect()[property] + window.pageYOffset;
    return elem.getBoundingClientRect()[property];
}

function toQueryString (data) {
    let option = data;
    let string = '';
    let optionLength = Object.keys(option).length;
    let i = 0;
    for (let key in option) {
        if ((Object.prototype.hasOwnProperty.call(option, key))) {
            i++;
            if (i >= optionLength) {
                string += key + '=' + option[key];
            } else {
                string += key + '=' + option[key] + '&';
            }
        }
    }
    return string;
}

const utils = {
    fetchJsToObj: fetchJsToObj,
    findHighestZIndex: findHighestZIndex,
    getDomPosition: getDomPosition,
    toQueryString: toQueryString
};

export { fetchJsToObj, findHighestZIndex, getDomPosition, toQueryString };
export { utils as default };