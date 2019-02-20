<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\View;

use QafooLabs\Bundle\NoFrameworkBundle\View\ExtraBundleInteropTemplateGuesser;
use PHPUnit\Framework\TestCase;
use QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ExtraBundleInteropTemplateGuesserTest extends TestCase
{
    public const ACTION = 'barAction';

    public const CONTROLLER = 'Foo';

    public const ENGINE = 'twig';

    public const FORMAT = 'html';

    public const TEMPLATE = '<my-template>';

    /**
     * @test
     * @dataProvider provideNonHandlingRequests
     */
    public function delegateOnRequestsWithoutTemplateAttribute(Request $request): void
    {
        $requestStack = new RequestStack();
        $requestStack->push($request);
        $delegate = $this->prophesize(TemplateGuesser::class);

        $delegate->guessControllerTemplateName(self::CONTROLLER, self::ACTION, self::FORMAT, self::ENGINE)
                 ->shouldBeCalled()
                 ->willReturn(self::TEMPLATE)
        ;

        $guesser = new ExtraBundleInteropTemplateGuesser($delegate->reveal(), $requestStack);
        self::assertSame(
          self::TEMPLATE,
          $guesser->guessControllerTemplateName(self::CONTROLLER, self::ACTION, self::FORMAT, self::ENGINE)
        );
    }

    /**
     * @test
     */
    public function returnTemplateAttributesTemplateWhenGiven(): void
    {
        $templateConfig = $this->prophesize(Template::class);
        $request = new Request();
        $request->attributes->set('_template', $templateConfig->reveal());

        $templateConfig->getTemplate()
                       ->shouldBeCalled()
                       ->willReturn(self::TEMPLATE)
        ;

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $delegate = $this->prophesize(TemplateGuesser::class);

        $delegate->guessControllerTemplateName()
                 ->shouldNotBeCalled()
        ;

        $guesser = new ExtraBundleInteropTemplateGuesser($delegate->reveal(), $requestStack);
        self::assertSame(
          self::TEMPLATE,
          $guesser->guessControllerTemplateName(self::CONTROLLER, self::ACTION, self::FORMAT, self::ENGINE)
        );
    }

    public function provideNonHandlingRequests(): array
    {
        $faultyAttributeRequest = new Request();
        $faultyAttributeRequest->attributes->set('_template', 'foobar');

        return [
          'simple request' => [new Request()],
          'request with incompatible template attribute' => [$faultyAttributeRequest],
        ];
    }
}
