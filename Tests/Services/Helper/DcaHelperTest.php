<?php

/**
 * @since       14.09.2024 - 14:53
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Datacollections\Classes\Enums\DcaConfig;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\DcaHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\TestCase;

class DcaHelperTest extends TestCase
{

    private $collectionFactory;

    private $table;


    private $field;


    private $arrayCollection;


    private $helper;



    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->table                = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->field                = $this->getMockBuilder(FieldnameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper               = new DcaHelper();

        $this->helper->setCollectionFactory($this->collectionFactory);
    }


    public function testGetDepandanciesReturnNullIfNoDcsFound(): void
    {
        $tablename = 'tl_testtable';
        unset($GLOBALS[DcaConfig::TL_DCA->name]);

        $this->table->expects(self::once())
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::never())
                    ->method('value');

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertNull($this->helper->getDepandancies($this->table, $this->field));
    }


    public function testGetDepandanciesReturnNullIfNoDcsForTableFound(): void
    {
        $tablename = 'tl_testtable';
        unset($GLOBALS[DcaConfig::TL_DCA->name]);
        $GLOBALS[DcaConfig::TL_DCA->name]['tl_files']['config'] = ['testConfig'];

        $this->table->expects(self::once())
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::never())
                    ->method('value');

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertNull($this->helper->getDepandancies($this->table, $this->field));
    }


    public function testGetDepandanciesReturnNullIfNoFieldsFound(): void
    {
        $tablename  = 'tl_testtable';
        $fieldname  = 'testfield';
        unset($GLOBALS[DcaConfig::TL_DCA->name]);
        $GLOBALS[DcaConfig::TL_DCA->name][$tablename][$fieldname]['palettes'] = '{title_legend},title;';

        $this->table->expects(self::once())
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::never())
                    ->method('value');

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertNull($this->helper->getDepandancies($this->table, $this->field));
    }


    public function testGetDepandanciesReturnNullIfFieldsConfigIsEmpty(): void
    {
        $tablename  = 'tl_testtable';

        unset($GLOBALS[DcaConfig::TL_DCA->name]);

        $GLOBALS[DcaConfig::TL_DCA->name][$tablename][DcaConfig::fields->name] = [];

        $this->table->expects(self::once())
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::never())
                    ->method('value');

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertNull($this->helper->getDepandancies($this->table, $this->field));
    }


    public function testGetDepandanciesReturnNullIfFieldHaveNoLazyLoadungConfig(): void
    {
        $tablename  = 'tl_testtable';
        $fieldname  = 'testfield';

        unset($GLOBALS[DcaConfig::TL_DCA->name]);

        $GLOBALS[DcaConfig::TL_DCA->name][$tablename][DcaConfig::fields->name][$fieldname] = [
            'label' => ['Test'],
        ];

        $this->table->expects(self::exactly(2))
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::once())
                    ->method('value')
                    ->willReturn($fieldname);

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertNull($this->helper->getDepandancies($this->table, $this->field));
    }


    public function testGetDepandanciesReturnArrayCollectionIfFieldHaveLazyLoadungConfig(): void
    {
        $tablename      = 'tl_testtable';
        $fieldname      = 'testfild';
        $lazyLoading    = [
            DcaConfig::table->name      => 'tl_files',
            DcaConfig::field->name      => 'id',
            DcaConfig::serialised->name => false
        ];

        unset($GLOBALS[DcaConfig::TL_DCA->name]);
        $GLOBALS[DcaConfig::TL_DCA->name][$tablename][DcaConfig::fields->name][$fieldname] = [
            'label'                         => ['Test'],
            DcaConfig::lazyloading->name    => $lazyLoading];

        $this->table->expects(self::exactly(2))
                    ->method('value')
                    ->willReturn($tablename);

        $this->field->expects(self::exactly(2))
                    ->method('value')
                    ->willReturn($fieldname);

        $this->collectionFactory->expects(self::once())
                                ->method('createArrayCollection')
                                ->with($lazyLoading)
                                ->willReturn($this->arrayCollection);

        $this->assertSame($this->arrayCollection, $this->helper->getDepandancies($this->table, $this->field));
    }
}
