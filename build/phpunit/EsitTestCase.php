<?php declare(strict_types=1);

/**
 * @author      pfroch <info@easySolutionsIT.de>
 * @link        http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT <#YEAR#>
 * @license     EULA
 * @version     2.0.0
 * @since       28.08.2024 - 12:20
 */

namespace Esit\Datacollections;

use Contao\TestCase\ContaoTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class EsitTestCase
 */
class EsitTestCase extends ContaoTestCase
{


    /**
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        //$this->initializeContao();
    }


    /**
     * setup the environment
     */
    protected function setUp(): void
    {
    }


    /**
     * tear down the environment
     */
    protected function tearDown(): void
    {
    }


    /**
     * Initialisiert Contao
     * @param string $tlMode
     * @param string $tlScript
     * @throws \Exception
     */
    protected function initializeContao($tlMode = 'TEST', $tlScript = 'EsitTestCase'): void
    {
        $framework = $this->mockContaoFramework();
        $framework->method('initialize');

        if (!defined('TL_MODE')) {
            define('TL_MODE', $tlMode);
            define('TL_SCRIPT', $tlScript);
            $initializePath = CONTAO_ROOT . "/system/initialize.php";

            if (is_file($initializePath)) {
                require($initializePath);
                stream_wrapper_restore('phar');// reregister stream wrapper for phpunit.phar
            } else {
                throw new \Exception(CONTAO_ROOT . "/system/initialize.php not found!");
            }
        }
    }


    /**
     * Ersatz für withConsecutive(), das in PHPUnit 9 deprecated ist!
     * Es wird bei jedem Aufruf immer der Wert aus $returnValues zurückgegeben.
     * @param MockObject $object
     * @param string $method
     * @param mixed $returnValue
     * @param array $expected
     * @return void
     * @example
     *  $expected = [['call-1_value-1', 'call-1_value-2'], ['call-2_value-1', 'call-2_value-2']]
     *  $this->addConsecutive($this->myMock, 'MethodName', $this->myMock, $expected);
     *
     * @deprecated use $this->consecutiveParams() instead
     */
    protected function addConsecutive(MockObject $object, string $method, mixed $returnValue, array $expected): void
    {
        $matcher = $this->exactly(\count($expected));

        $object->expects($matcher)
               ->method($method)
               ->with(
                   $this->callback(
                       function(... $param) use ($matcher, $expected) {
                           $count = $matcher->getInvocationCount() - 1;

                           foreach ($param as $i => $v) {

                               $this->assertSame($expected[$count][$i], $v);
                           }

                           return true;
                       }
                   )
               )
               ->willReturn($returnValue);
    }


    /**
     * Ersatz für withConsecutive(), das in PHPUnit 9 deprecated ist!
     * Es wird nichts zurückgegeben.
     * @param MockObject $object
     * @param string $method
     * @param array $expected
     * @return void
     * @example
     *  $expected = [['call-1_value-1', 'call-1_value-2'], ['call-2_value-1', 'call-2_value-2']]
     *  $this->addConsecutive($this->myMock, 'MethodName', $expected);
     *
     * @deprecated use $this->consecutiveParams() instead
     */
    protected function addConsecutiveVoid(MockObject $object, string $method,array $expected): void
    {
        $matcher = $this->exactly(\count($expected));

        $object->expects($matcher)
               ->method($method)
               ->with(
                   $this->callback(
                       function(... $param) use ($matcher, $expected) {
                           $count = $matcher->getInvocationCount() - 1;

                           foreach ($param as $i => $v) {

                               $this->assertSame($expected[$count][$i], $v);
                           }

                           return true;
                       }
                   )
               );
    }


    /**
     * Ersatz für withConsecutive(), das in PHPUnit 9 deprecated ist!
     * Es wird bei jedem Aufruf ein Wert aus $returnValues zurückgegeben.
     * @param MockObject $object
     * @param string $method
     * @param mixed $returnValue
     * @param array $expected
     * @return void
     * @example
     *  $expected = [['call-1_value-1', 'call-1_value-2'], ['call-2_value-1', 'call-2_value-2']]
     *  $return   = ['retrun1', 'return2'];
     *  $this->addConsecutive($this->myMock, 'MethodName', $return, $expected);
     *
     * @deprecated use $this->consecutiveParams() instead
     */
    protected function addConsecutiveReturn(
        MockObject $object,
        string $method,
        mixed $returnValues,
        array $expected
    ): void {
        $matcher = $this->exactly(\count($expected));

        $object->expects($matcher)
               ->method($method)
               ->with(
                   $this->callback(
                       function(... $param) use ($matcher, $expected) {
                           $count = $matcher->getInvocationCount() - 1;

                           foreach ($param as $i => $v) {

                               $this->assertSame($expected[$count][$i], $v);
                           }

                           return true;
                       }
                   )
               )
               ->willReturnOnConsecutiveCalls(...$returnValues);
    }





    /**
     * Ersetzt withConsecutive()
     * @param array ...$args
     * @return array
     * @see https://gist.github.com/ziadoz/370fe63e24f31fd1eb989e7477b9a472
     *
     * @example
     * $mock = $this->getMockBuilder(SomeClass::class)->getMock();
     *
     * $mock->expects($this->exactly(3))
     *      ->method('add')
     *      ->with(... $this->consecutiveParams(
     *          ['meta'],
     *          ['title'],
     *          ['caption'],
     *          ['alt']
     *      ))
     *      ->willReturnOnConsecutiveCalls(
     *          $meta, '', '', ''
     *      );
     */
    public function consecutiveParams(array ...$args): array
    {
        $callbacks = [];
        $count = count(max($args));

        for ($index = 0; $index < $count; $index++) {
            $returns = [];

            foreach ($args as $arg) {
                if (! array_is_list($arg)) {
                    throw new \InvalidArgumentException('Every array must be a list');
                }

                if (! isset($arg[$index])) {
                    throw new \InvalidArgumentException(sprintf('Every array must contain %d parameters', $count));
                }

                $returns[] = $arg[$index];
            }

            $callbacks[] = $this->callback(new class ($returns) {
                public function __construct(protected array $returns)
                {
                }

                public function __invoke(mixed $actual): bool
                {
                    if (count($this->returns) === 0) {
                        return true;
                    }

                    $next = array_shift($this->returns);
                    if ($next instanceof Constraint) {
                        $next->evaluate($actual);
                        return true;
                    }

                    return $actual === $next;
                }
            });
        }

        return $callbacks;
    }
}

