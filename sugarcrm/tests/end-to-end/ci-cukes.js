/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
'use strict';

const _ = require('lodash');
const path = require('path');
const ciConfig = require('./ci-config.js');
const Cukes = require('@sugarcrm/seedbed');
const ciUtils = Cukes.CIUtils;
const utils = Cukes.Utils;
const chalk = utils.chalk;
const { Generator, Promise } = Cukes.PromiseGenerator;
const fs = Promise.promisifyAll(require('fs-extra'));

let zipResults = () => {
    return ciUtils.zipCiResults(
        {
            testsOutputFolders: ciConfig.resultsConfig,
            resultsFolder: ciConfig.resultsFolder
        })
        .then(() => {
            console.log(`zip artifacts: ${chalk.green('success')}`);
        });
};

module.exports = {
    run() {
        //create results folder
        fs.emptyDirSync(ciConfig.resultsFolder);

        //start CI procedure
        Generator.run(function*() {
            try {
                try {

                    yield Cukes.CI.ci(ciConfig);

                } finally {

                    yield zipResults();
                    yield fs.removeAsync(path.resolve(ciConfig.resultsFolder));
                }
            } catch (error) {
                console.log(`${error.logs || error.stack || ''}`);
                process.exit(1);
            }
        });

    }
};

//handle errors
process.on('unhandledRejection', (error, p) => {
    console.log(`Unhandled Rejection at: Promise ${p}
${error.stack || error}`);
    zipResults()
        .then(() => {
            process.exit(1);
        });
});
