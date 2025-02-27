<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Components\Plugin;

use PHPUnit\Framework\TestCase;
use Shopware\Components\Plugin\ResourceSubscriber;
use Shopware\Components\Theme\LessDefinition;

class ResourceSubscriberTest extends TestCase
{
    public function testEmptyPlugin(): void
    {
        $subscriber = new ResourceSubscriber(__DIR__ . '/examples/EmptyPlugin');

        static::assertNull($subscriber->onCollectCss());
        static::assertNull($subscriber->onCollectJavascript());
        static::assertNull($subscriber->onCollectLess());

        $subscriberWithViews = new ResourceSubscriber(__DIR__ . '/examples/EmptyPlugin');
        $templateEventArgs = new \Enlight_Event_EventArgs();
        $subscriberWithViews->onRegisterTemplate($templateEventArgs);
        static::assertIsArray($templateEventArgs->getReturn());
        static::assertNotEmpty($templateEventArgs->getReturn());
    }

    public function testFoo(): void
    {
        $subscriber = new ResourceSubscriber(__DIR__ . '/examples/TestPlugin');

        static::assertSame(
            [
                __DIR__ . '/examples/TestPlugin/Resources/frontend/css/foo/bar.css',
                __DIR__ . '/examples/TestPlugin/Resources/frontend/css/test.css',
            ],
            $subscriber->onCollectCss()->toArray()
        );

        static::assertSame(
            [
                __DIR__ . '/examples/TestPlugin/Resources/frontend/js/foo.js',
                __DIR__ . '/examples/TestPlugin/Resources/frontend/js/foo/bar.js',
            ],
            $subscriber->onCollectJavascript()->toArray()
        );

        static::assertEquals(
            new LessDefinition([], [
                __DIR__ . '/examples/TestPlugin/Resources/frontend/less/all.less',
            ]),
            $subscriber->onCollectLess()
        );

        $subscriberWithViews = new ResourceSubscriber(__DIR__ . '/examples/TestPlugin');
        $templateEventArgs = new \Enlight_Event_EventArgs();
        $subscriberWithViews->onRegisterTemplate($templateEventArgs);
        static::assertTrue(\is_array($templateEventArgs->getReturn()));
        static::assertSame(
            [
                __DIR__ . '/examples/TestPlugin/Resources/views',
            ],
            $templateEventArgs->getReturn()
        );
    }
}
