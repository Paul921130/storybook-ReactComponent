import React from 'react';
import DateBar from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('date_bar', () => (
        <div>
            <h3>date_bar的元件</h3>
            <DateBar fakeProps='fakeProps'/>
        </div>
    ));
