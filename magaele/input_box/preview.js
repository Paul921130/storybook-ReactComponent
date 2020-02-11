import React from 'react';
import InputBox from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('待測試功能', module)
    .add('input_box', () => (
        <div>
            <InputBox/>
        </div>
    ));
