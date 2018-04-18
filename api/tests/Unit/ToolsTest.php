<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;
use App\Helpers\Tools;

class Foo {
    public $prop1 = 'hello';
}

class ToolsTest extends TestCase{

    public function test_parseRole(){
        $roles = 'api/v1/address/list,api/v1/address/obj,api/v1/address/add,api/v1/address/edit,api/v1/address/remove,api/v1/article/list,api/v1/article/obj,api/v1/attach/list,api/v1/article/obj,api/v1/attach/hello-list';
        $output = Tools::parseRole($roles);
        $eput = ['address', 'article', 'attach', 'attach/hello'];
        $this->assertEquals($output, $eput);

        $roles = '';
        $output = Tools::parseRole($roles);
        $eput = [];
        $this->assertEquals($output, $eput);

        $roles = null;
        $output = Tools::parseRole($roles);
        $eput = [];
        $this->assertEquals($output, $eput);

        $roles = 'api/v1/address/obj';
        $output = Tools::parseRole($roles);
        $eput = [];
        $this->assertEquals($output, $eput);
    }

    public function test_getProp(){
    	$inputArr = ['prop1' => 'hello'];
    	$inputObj = New Foo;

        $this->assertTrue(Tools::getProp($inputArr, 'prop1') === 'hello');
        $this->assertTrue(Tools::getProp($inputArr, 'prop2') === null);
        $this->assertTrue(Tools::getProp($inputArr, 'prop2', 'farewell') === 'farewell');

        $this->assertTrue(Tools::getProp($inputObj, 'prop1') === 'hello');
        $this->assertTrue(Tools::getProp($inputObj, 'prop2') === null);
        $this->assertTrue(Tools::getProp($inputObj, 'prop2', 'farewell') === 'farewell');
    }

    public function test_camelToWords(){
        $input = 'toiLaTranBacSon';
        $output = Tools::camelToWords($input);

        $result = $output === 'toi La Tran Bac Son';
        $this->assertTrue($result);

        $output = Tools::camelToWords($input, true);
        $result = $output === 'Toi La Tran Bac Son';
        $this->assertTrue($result);
    }

    public function test_getListKey(){
        $input = ['key1' => 'a', 'key2' => 'b', 'key3' => 'c'];
        $output = Tools::getListKey($input);

        $result = $output === ['key1', 'key2', 'key3'];
        $this->assertTrue($result);
    }

    public function test_errMessage(){
        try{
            $input = ['hello'];
            $input->test();
        }catch(\Error $e){
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();

            $output = Tools::errMessage($e);

            $result = $output === $errorMessage.' => '.$errorFile.' [line '.$errorLine.']';
            $this->assertTrue($result);
        }
    }

    public function test_niceUrl(){
        $input = "Trần Bắc Sơn ----- nguyễn trọng ngẫm mệt";
        $output = Tools::niceUrl($input);
        $result = $output === 'tran-bac-son-nguyen-trong-ngam-met';
        $this->assertTrue($result);

        $output = Tools::niceUrl($input, false);

        $result = $output === 'tran bac son nguyen trong ngam met';
        $this->assertTrue($result);
    }

    public function test_getFileExtension(){
        $input = '/abc/def/test.jpg';
        $output = Tools::getFileExtension($input);
        $result = $output === 'jpg';
        $this->assertTrue($result);

        $input = '/abc/def/test' ;
        $output = Tools::getFileExtension($input);
        $result = $output === 'tmp';
        $this->assertTrue($result);

        $input = '' ;
        $output = Tools::getFileExtension($input);
        $result = $output === 'tmp';
        $this->assertTrue($result);

        $input = 5 ;
        $output = Tools::getFileExtension($input);
        $result = $output === 'tmp';
        $this->assertTrue($result);
    }

    public function test_getThumbnail(){
        $input = '/abc/def/test.jpg';
        $output = Tools::getThumbnail($input);
        $result = $output === '/abc/def/test-thumbnail.jpg';
        $this->assertTrue($result);

        $input = '/abc/def/test.bak.jpg';
        $output = Tools::getThumbnail($input);
        $result = $output === '/abc/def/test.bak-thumbnail.jpg';
        $this->assertTrue($result);

        $input = '';
        $output = Tools::getThumbnail($input);
        $result = $output === '';
        $this->assertTrue($result);

        $input = 5;
        $output = Tools::getThumbnail($input);
        $result = $output === '5-thumbnail';
        $this->assertTrue($result);
    }

    public function test_prepareBase64ToFile(){
        $input = 'data:image/gif;base64,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';

        $output = Tools::prepareBase64ToFile($input);
        $result = $output === [
            'ext' => 'gif',
            'data' => 'R0lGODlhPQBEAPeoAJosM//AwO/AwHVYZ/z595kzAP/s7P+goOXMv8+fhw/v739/f+8PD98fH/8mJl+fn/9ZWb8/PzWlwv///6wWGbImAPgTEMImIN9gUFCEm/gDALULDN8PAD6atYdCTX9gUNKlj8wZAKUsAOzZz+UMAOsJAP/Z2ccMDA8PD/95eX5NWvsJCOVNQPtfX/8zM8+QePLl38MGBr8JCP+zs9myn/8GBqwpAP/GxgwJCPny78lzYLgjAJ8vAP9fX/+MjMUcAN8zM/9wcM8ZGcATEL+QePdZWf/29uc/P9cmJu9MTDImIN+/r7+/vz8/P8VNQGNugV8AAF9fX8swMNgTAFlDOICAgPNSUnNWSMQ5MBAQEJE3QPIGAM9AQMqGcG9vb6MhJsEdGM8vLx8fH98AANIWAMuQeL8fABkTEPPQ0OM5OSYdGFl5jo+Pj/+pqcsTE78wMFNGQLYmID4dGPvd3UBAQJmTkP+8vH9QUK+vr8ZWSHpzcJMmILdwcLOGcHRQUHxwcK9PT9DQ0O/v70w5MLypoG8wKOuwsP/g4P/Q0IcwKEswKMl8aJ9fX2xjdOtGRs/Pz+Dg4GImIP8gIH0sKEAwKKmTiKZ8aB/f39Wsl+LFt8dgUE9PT5x5aHBwcP+AgP+WltdgYMyZfyywz78AAAAAAAD///8AAP9mZv///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAKgALAAAAAA9AEQAAAj/AFEJHEiwoMGDCBMqXMiwocAbBww4nEhxoYkUpzJGrMixogkfGUNqlNixJEIDB0SqHGmyJSojM1bKZOmyop0gM3Oe2liTISKMOoPy7GnwY9CjIYcSRYm0aVKSLmE6nfq05QycVLPuhDrxBlCtYJUqNAq2bNWEBj6ZXRuyxZyDRtqwnXvkhACDV+euTeJm1Ki7A73qNWtFiF+/gA95Gly2CJLDhwEHMOUAAuOpLYDEgBxZ4GRTlC1fDnpkM+fOqD6DDj1aZpITp0dtGCDhr+fVuCu3zlg49ijaokTZTo27uG7Gjn2P+hI8+PDPERoUB318bWbfAJ5sUNFcuGRTYUqV/3ogfXp1rWlMc6awJjiAAd2fm4ogXjz56aypOoIde4OE5u/F9x199dlXnnGiHZWEYbGpsAEA3QXYnHwEFliKAgswgJ8LPeiUXGwedCAKABACCN+EA1pYIIYaFlcDhytd51sGAJbo3onOpajiihlO92KHGaUXGwWjUBChjSPiWJuOO/LYIm4v1tXfE6J4gCSJEZ7YgRYUNrkji9P55sF/ogxw5ZkSqIDaZBV6aSGYq/lGZplndkckZ98xoICbTcIJGQAZcNmdmUc210hs35nCyJ58fgmIKX5RQGOZowxaZwYA+JaoKQwswGijBV4C6SiTUmpphMspJx9unX4KaimjDv9aaXOEBteBqmuuxgEHoLX6Kqx+yXqqBANsgCtit4FWQAEkrNbpq7HSOmtwag5w57GrmlJBASEU18ADjUYb3ADTinIttsgSB1oJFfA63bduimuqKB1keqwUhoCSK374wbujvOSu4QG6UvxBRydcpKsav++Ca6G8A6Pr1x2kVMyHwsVxUALDq/krnrhPSOzXG1lUTIoffqGR7Goi2MAxbv6O2kEG56I7CSlRsEFKFVyovDJoIRTg7sugNRDGqCJzJgcKE0ywc0ELm6KBCCJo8DIPFeCWNGcyqNFE06ToAfV0HBRgxsvLThHn1oddQMrXj5DyAQgjEHSAJMWZwS3HPxT/QMbabI/iBCliMLEJKX2EEkomBAUCxRi42VDADxyTYDVogV+wSChqmKxEKCDAYFDFj4OmwbY7bDGdBhtrnTQYOigeChUmc1K3QTnAUfEgGFgAWt88hKA6aCRIXhxnQ1yg3BCayK44EWdkUQcBByEQChFXfCB776aQsG0BIlQgQgE8qO26X1h8cEUep8ngRBnOy74E9QgRgEAC8SvOfQkh7FDBDmS43PmGoIiKUUEGkMEC/PJHgxw0xH74yx/3XnaYRJgMB8obxQW6kL9QYEJ0FIFgByfIL7/IQAlvQwEpnAC7DtLNJCKUoO/w45c44GwCXiAFB/OXAATQryUxdN4LfFiwgjCNYg+kYMIEFkCKDs6PKAIJouyGWMS1FSKJOMRB/BoIxYJIUXFUxNwoIkEKPAgCBZSQHQ1A2EWDfDEUVLyADj5AChSIQW6gu10bE/JG2VnCZGfo4R4d0sdQoBAHhPjhIB94v/wRoRKQWGRHgrhGSQJxCS+0pCZbEhAAOw=='
        ];
        $this->assertTrue($result);
    }

    public function test_getImgUrlInfo(){
        $input = 'http://abc.def/some/test.jpg';
        $output = Tools::getImgUrlInfo($input);
        $result = $output === [
            'ext' => 'jpg',
            'name' => 'test'
        ];
        $this->assertTrue($result);

        $input = 'http://abc.def/some/test';
        $output = Tools::getImgUrlInfo($input);
        $result = $output === [
            'ext' => null,
            'name' => 'test'
        ];
        $this->assertTrue($result);

        $input = '';
        $output = Tools::getImgUrlInfo($input);
        $result = $output === [
            'ext' => null,
            'name' => null
        ];
        $this->assertTrue($result);
    }

    public function test_carbonToYearMonthDay(){
        $input = Carbon::now();
        $input->day = 12;
        $input->month = 11;
        $input->year = 2008;

        $output = Tools::carbonToYearMonthDay($input);
        $eput = 20081112;
        $this->assertEquals($output, $eput);

        $input = Carbon::now();
        $input->day = 1;
        $input->month = 1;
        $input->year = 2008;

        $output = Tools::carbonToYearMonthDay($input);
        $eput = 20080101;
        $this->assertEquals($output, $eput);
    }

    public function test_parseOrderBy(){
        $input = 'id';
        $eput = ['id', 'asc'];
        $output = Tools::parseOrderBy($input);
        $this->assertEquals($output, $eput);
    }

    public function test_getKeyValuePair(){
        $input = ['key' => 'key 1', 'value' => 'value 1'] ;
        $output = Tools::getKeyValuePair('key', $input);
        $eput = ['key' => 'key 1'];
        $this->assertEquals($output, $eput);
    }

    public function test_dateForStatistics(){
        $today = Tools::nowDate();
        $dayOfWeek = $today->dayOfWeek; // [sun: 0, mon: 1, tue: 2, ...., sat: 6]
        if(!$dayOfWeek){
            $dayOfWeek = 6;
        }else{
            $dayOfWeek--;
        }
        $dateType = 'today';
        $output = Tools::dateForStatistics($dateType);
        $eput = [
            'startDate' => $today,
            'endDate' => $today
        ];
        $this->assertEquals($output, $eput);
        $this->assertEquals($output['startDate'], $output['endDate']);

        # Monday to current day
        $dateType = 'this_week';
        $output = Tools::dateForStatistics($dateType);
        $this->assertEquals($output['endDate'], $today);
        $this->assertEquals($output['startDate'], $output['endDate']->subDay($dayOfWeek));
    }

    public function test_roundThousand(){
        $output = Tools::roundThousand(2017);
        $eput = 2000;
        $this->assertEquals($output, $eput);

        $output = Tools::roundThousand(2017.134567);
        $eput = 2000;
        $this->assertEquals($output, $eput);

        $output = Tools::roundThousand(999);
        $eput = 1000;
        $this->assertEquals($output, $eput);

        $output = Tools::roundThousand(499);
        $eput = 0;
        $this->assertEquals($output, $eput);

        $output = Tools::roundThousand(0);
        $eput = 0;
        $this->assertEquals($output, $eput);
    }

    public function test_getDomainFromUrl(){
        // https://shop123159106.taobao.com -> shop123159106.taobao.com
        $input = 'https://shop123159106.taobao.com';
        $output = Tools::getDomainFromUrl($input);
        $eput = 'shop123159106.taobao.com';
        $this->assertEquals($output, $eput);

        $input = 'https://shop123159106.taobao.com/';
        $output = Tools::getDomainFromUrl($input);
        $eput = 'shop123159106.taobao.com';
        $this->assertEquals($output, $eput);

        $input = 'https://shop123159106.taobao.com/abc/def?hello=4';
        $output = Tools::getDomainFromUrl($input);
        $eput = 'shop123159106.taobao.com';
        $this->assertEquals($output, $eput);
    }

    public function test_move_uploaded_file(){
        $sourceImg = config('app.static_root').'test/source/img.jpg';
        $outputImg = config('app.static_root').'test/output/img.jpg';
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $this->assertEquals(md5_file($sourceImg), md5_file($outputImg));
        @unlink($outputImg);
    }

    public function test_scaleImage(){
        $sourceImg = config('app.static_root').'test/source/img.jpg';
        $outputImg = config('app.static_root').'test/output/img.jpg';
        Tools::move_uploaded_file($sourceImg, $outputImg);

        $imageInfo = getimagesize($outputImg);
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        # No new width
        $output = Tools::scaleImage($outputImg);
        $eput = $width/$height;
        $this->assertEquals($output, $eput);
        @unlink($outputImg);

        # New width
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $newWidth = 100;
        $output = Tools::scaleImage($outputImg, $newWidth);
        $eput = $width/$height;
        $this->assertEquals($output, $eput);
        $this->assertEquals(getimagesize($outputImg)[0], $newWidth);
        @unlink($outputImg);

        # New width + thumbnail
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $newWidth = 100;
        $output = Tools::scaleImage($outputImg, $newWidth, true);
        $eput = $width/$height;
        $this->assertEquals($output, $eput);
        $this->assertEquals(getimagesize($outputImg)[0], $newWidth);
        $this->assertEquals(file_exists(Tools::getThumbnail($outputImg)), true);
        @unlink($outputImg);
        @unlink(Tools::getThumbnail($outputImg));

        # Golden ratio
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $output = Tools::scaleImage($outputImg, null, false, true);
        $eput = config('upload.golden_ratio');
        $this->assertEquals($output, $eput);
        @unlink($outputImg);

        # Golden ratio with thumbnail
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $output = Tools::scaleImage($outputImg, null, true, true);
        $eput = config('upload.golden_ratio');
        $this->assertEquals($output, $eput);
        $this->assertEquals(file_exists(Tools::getThumbnail($outputImg)), true);
        @unlink($outputImg);
        @unlink(Tools::getThumbnail($outputImg));

        # Golden ratio with thumbnail and resize
        Tools::move_uploaded_file($sourceImg, $outputImg);
        $newWidth = 100;
        $output = Tools::scaleImage($outputImg, $newWidth, true, true);
        $eput = config('upload.golden_ratio');
        $this->assertEquals($output, $eput);
        $this->assertEquals(getimagesize($outputImg)[0], $newWidth);
        $this->assertEquals(file_exists(Tools::getThumbnail($outputImg)), true);
        @unlink($outputImg);
        @unlink(Tools::getThumbnail($outputImg));
    }

    public function test_uploadHandler(){
        # uploadHandler($file, $existFile, $fieldName, $destination, $width=0, $thumbnail=false, $golden=false)
        $sourceImg = config('app.static_root').'test/source/img.jpg';
        $file = array(
            'thumbnail' => array(
                'name' => 'img.jpg',
                'type' => 'image/jpeg',
                'size' => 90836,
                'tmp_name' => $sourceImg,
                'error' => 0
            )
        );
        /*
        $eput = [
            'path' => '',
            'type' => '',
            'ratio' => 1,
            'success' => true,
            'message' => ''
        ];
        */
        # Normal upload without exist file
        $existFile = false;
        $fieldName = 'thumbnail';
        $destination = 'test';
        $output = Tools::uploadHandler($file, $existFile, $fieldName, $destination);
        $outputFile = config('app.media_root').$output['path'];
        $imageInfo = getimagesize($sourceImg);
        $this->assertEquals(file_exists($outputFile), true);
        $this->assertEquals($output['type'], 'image');
        $this->assertEquals($output['ratio'], $imageInfo[0]/$imageInfo[1]);
        @unlink($outputFile);

        # Normal upload with exist file
        Tools::move_uploaded_file($sourceImg, config('app.media_root').'test/img.jpg');
        $existFile = 'test/img.jpg';
        $fieldName = 'thumbnail';
        $destination = 'test';
        $output = Tools::uploadHandler($file, $existFile, $fieldName, $destination);
        $outputFile = config('app.media_root').$output['path'];
        $imageInfo = getimagesize($sourceImg);
        $this->assertEquals(file_exists($outputFile), true);
        $this->assertEquals($output['type'], 'image');
        $this->assertEquals($output['ratio'], $imageInfo[0]/$imageInfo[1]);
        @unlink($outputFile);


        # Normal upload without exist file, set new width
        $newWidth = 100;
        $existFile = false;
        $fieldName = 'thumbnail';
        $destination = 'test';
        $output = Tools::uploadHandler($file, $existFile, $fieldName, $destination, $newWidth);
        $outputFile = config('app.media_root').$output['path'];
        $imageInfo = getimagesize($sourceImg);
        $resultImageInfo = getimagesize($outputFile);
        $this->assertEquals(file_exists($outputFile), true);
        $this->assertEquals($output['type'], 'image');
        $this->assertEquals($output['ratio'], $imageInfo[0]/$imageInfo[1]);
        $this->assertEquals($resultImageInfo[0], $newWidth);
        @unlink($outputFile);

        # Normal upload without exist file, set new width, has thumbnail
        $newWidth = 100;
        $existFile = false;
        $fieldName = 'thumbnail';
        $destination = 'test';
        $output = Tools::uploadHandler($file, $existFile, $fieldName, $destination, $newWidth, true);
        $outputFile = config('app.media_root').$output['path'];
        $imageInfo = getimagesize($sourceImg);
        $resultImageInfo = getimagesize($outputFile);
        $this->assertEquals(file_exists($outputFile), true);
        $this->assertEquals($output['type'], 'image');
        $this->assertEquals($output['ratio'], $imageInfo[0]/$imageInfo[1]);
        $this->assertEquals($resultImageInfo[0], $newWidth);
        $this->assertEquals(file_exists(Tools::getThumbnail($outputFile)), true);
        @unlink($outputFile);
        @unlink(Tools::getThumbnail($outputFile));

        # Normal upload without exist file, set new width, has thumbnail, golden ratio
        $newWidth = 100;
        $existFile = false;
        $fieldName = 'thumbnail';
        $destination = 'test';
        $output = Tools::uploadHandler($file, $existFile, $fieldName, $destination, $newWidth, true, true);
        $outputFile = config('app.media_root').$output['path'];
        $imageInfo = getimagesize($sourceImg);
        $resultImageInfo = getimagesize($outputFile);
        $this->assertEquals(file_exists($outputFile), true);
        $this->assertEquals($output['type'], 'image');
        $this->assertEquals($output['ratio'], config('upload.golden_ratio'));
        $this->assertEquals($resultImageInfo[0], $newWidth);
        $this->assertEquals(file_exists(Tools::getThumbnail($outputFile)), true);
        @unlink($outputFile);
        @unlink(Tools::getThumbnail($outputFile));
    }

    public function test_ignoreKeys(){
        $input = [
            'key1' => 'value 1',
            'key2' => 'value 2',
            'key3' => 'value 3',
            'key4' => 'value 4'
        ];
        $keys = ['key2', 'key4'];
        $output = Tools::ignoreKeys($input, $keys);
        $eput = [
            'key1' => 'value 1',
            'key3' => 'value 3'
        ];
        $this->assertEquals($output, $eput);

        $keys = [];
        $output = Tools::ignoreKeys($input, $keys);
        $eput = [
            'key1' => 'value 1',
            'key2' => 'value 2',
            'key3' => 'value 3',
            'key4' => 'value 4'
        ];
        $this->assertEquals($output, $eput);

        $keys = ['new_key'];
        $output = Tools::ignoreKeys($input, $keys);
        $eput = [
            'key1' => 'value 1',
            'key2' => 'value 2',
            'key3' => 'value 3',
            'key4' => 'value 4'
        ];
        $this->assertEquals($output, $eput);
    }

    public function test_removeNullKey(){
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => null,
            'd' => 'test'
        ];
        $output = Tools::removeNullKeys($input);
        $eput = [
            'a' => 1,
            'b' => 2,
            'd' => 'test'
        ];
        $this->assertEquals($output, $eput);

        $input = [
            'a' => null,
            'b' => null,
            'c' => null,
            'd' => null
        ];
        $output = Tools::removeNullKeys($input);
        $eput = [
        ];
        $this->assertEquals($output, $eput);

        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 'null',
            'd' => 'test'
        ];
        $output = Tools::removeNullKeys($input);
        $eput = [
            'a' => 1,
            'b' => 2,
            'c' => 'null',
            'd' => 'test'
        ];
        $this->assertEquals($output, $eput);
    }

    public function test_isInt(){
        $output = Tools::isInt('8.9');
        $eput = false ;
        $this->assertEquals($output, $eput);

        $output = Tools::isInt(8.9);
        $eput = false ;
        $this->assertEquals($output, $eput);

        $output = Tools::isInt('8');
        $eput = true ;
        $this->assertEquals($output, $eput);

        $output = Tools::isInt(8);
        $eput = true ;
        $this->assertEquals($output, $eput);

        $output = Tools::isInt('a');
        $eput = false ;
        $this->assertEquals($output, $eput);

        $output = Tools::isInt(null);
        $eput = false ;
        $this->assertEquals($output, $eput);
    }

    public function test_getVendorFromUrl(){
        $url ='https://detail.1688.com/offer/537093909020.html?spm=a2604.8164724.2077238.13.rPHVUU';
        $output = Tools::getVendorFromUrl($url);
        $this->assertEquals($output, '1688');

        $url ='https://world.taobao.com/item/20107351902.htm?fromSite=main&ali_trackid=2:mm_34144973_12512052_47304144:1490931246_2k3_1493533466&spm=daogou.8208616.1.2.2HFuVX';
        $output = Tools::getVendorFromUrl($url);
        $this->assertEquals($output, 'TAOBAO');

        $url ='https://world.tmall.com/item/529692780933.htm?spm=a21bp.8294655.topsale.2.U0NvHu';
        $output = Tools::getVendorFromUrl($url);
        $this->assertEquals($output, 'TMALL');
    }
}
