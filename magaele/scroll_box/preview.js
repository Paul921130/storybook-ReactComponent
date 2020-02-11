import React from 'react';
import ScrollBox from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('待測試功能', module)
    .add('scroll_box', () => (
        <div>
            <ScrollBox headerH="60px"/>
        </div>
    ));
