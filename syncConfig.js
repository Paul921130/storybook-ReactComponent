const fs = require('fs');
const path = require('path');
const chalk = require('chalk');
const configPath = path.resolve(__dirname, './.storybook/config.js');
const excludeDir = /\.js|core/g;

function addReq (magaele, data) {
    let newData = data.replace(/(\(\) +\{[\n\s\w\W]*)(\})/gi, `$1\n\trequire('../magaele/${magaele}/preview.js');$2`);
    return newData;
}

fs.readdir('./magaele', (err, files) => {

    if (err) throw err;

    const newArray = files.filter((ele, index) => {
        return !excludeDir.test(ele);
    });

    fs.readFile(configPath, 'utf8', (err, data) => {
        if (err) throw err;
        let newData = data.replace(/(\(\) +\{)[\n\s\w\W]+(\})/g, '$1$2');
        newArray.map((ele, index) => {
            newData = addReq(ele, newData);
            return null;
        });
        fs.writeFile(configPath, newData, (err) => {
            if (err) throw err;
            console.log(chalk.green('已同步magaele至.storybook/config.js'));
        });
    });

});