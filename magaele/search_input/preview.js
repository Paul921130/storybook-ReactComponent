import React from 'react';
import SearchInput from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('search_input', () => (
        <div>
            <SearchInput/>
        </div>
    ));
