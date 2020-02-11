import React from 'react';
import TabPage from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('tab_page', () => (
        <div>
            <h3>tab_page元件</h3>
            <TabPage/>
        </div>
    ));
