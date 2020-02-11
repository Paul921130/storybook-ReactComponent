import React from 'react';
import CitytabContainer from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(組件)', module)
    .add('citytab_container', () => (
        <div>
            <CitytabContainer/>
        </div>
    ));
