/**
 * github地址:https://github.com/ZQCard/design_pattern
 * 过滤器模式（Filter Pattern）或标准模式（Criteria Pattern）是一种设计模式， * 这种模式允许开发人员使用不同的标准来过滤一组对象，通过逻辑运算以解耦的方式把它们连接起来。 
 * 这种类型的设计模式属于结构型模式，它结合多个标准来获得单一标准。* 例子:筛选男人、女人、单身男人、单身女人
 *
*/

     (1)Person.class.php（对象类）

<?php

namespace Filter;

class Person
{
    private $name;
    private $gender;
    private $maritalStatus;

    public function __construct($name, $gender, $maritalStatus)
    {
        $this->name = $name;
        $this->gender = $gender;
        $this->maritalStatus = $maritalStatus;
    }

    public function __get($attributes)
    {
        return $this->$attributes;
    }
}


         （2）Criteria.class.php（抽象接口,规范实现类）
<?php

namespace Filter;

interface Criteria
{
    public function meetCriteria($persons);
}

          （3）Male.class.php（男性类筛选）


<?php

namespace Filter;

class CriteriaMale implements Criteria
{
    public function meetCriteria($persons)
    {
        $malePerson = [];
        foreach ($persons as $person) {
            if (strtoupper($person->gender) == 'MALE') {
                $malePerson[] = $person;
            }
        }
        return $malePerson;
    }
}

（4）Female.class.php（女性类筛选）
<?php

namespace Filter;

class CriteriaFemale implements Criteria
{
    public function meetCriteria($persons)
    {
        $femalePerson = [];
        foreach ($persons as $person) {
            if (strtoupper($person->gender) == 'FEMALE') {
                $femalePerson[] = $person;
            }
        }
        return $femalePerson;
    }
}

（5）Single.class.php（单身类筛选）

<?php

namespace Filter;

class CriteriaSingle implements Criteria
{
    public function meetCriteria($persons)
    {
        $singlePerson = [];
        foreach ($persons as $person) {
            if (strtoupper($person->maritalStatus) == 'SINGLE') {
                $singlePerson[] = $person;
            }
        }
        return $singlePerson;
    }
}

（6）OrCriteria.class.php（或者条件筛选）
<?php

namespace Filter;

class OrCriteria implements Criteria
{
    private $criteria;
    private $otherCriteria;

    public function __construct(Criteria $criteria, Criteria $otherCriteria)
    {
        $this->criteria = $criteria;
        $this->otherCriteria = $otherCriteria;
    }

    public function meetCriteria($persons)
    {
        $firstCriteriaItems = $this->criteria->meetCriteria($persons);
        $otherCriteriaItems = $this->otherCriteria->meetCriteria($persons);

        foreach ($otherCriteriaItems as $person) {
            if (!in_array($person, $firstCriteriaItems)) {
                $firstCriteriaItems[] = $person;
            }
        }

        return $firstCriteriaItems;
    }
}


（6）AndCriteria.class.php（并且条件筛选）

<?php

namespace Filter;

class AndCriteria implements Criteria
{
    private $criteria;
    private $otherCriteria;

    public function __construct(Criteria $criteria,Criteria $otherCriteria)
    {
        $this->criteria = $criteria;
        $this->otherCriteria = $otherCriteria;
    }

    public function meetCriteria($persons)
    {
        $firstCriteriaPerson = $this->criteria->meetCriteria($persons);
        return $this->otherCriteria->meetCriteria($firstCriteriaPerson);
    }
}

(7)filter.php(客户端)
<?php
spl_autoload_register(function ($className){
    $className = str_replace('\\','/',$className);
    include $className.".class.php";
});

use Filter\Person;
use Filter\CriteriaMale;
use Filter\CriteriaFemale;
use Filter\CriteriaSingle;
use Filter\AndCriteria;
use Filter\OrCriteria;

$persons = [];
$persons[] = (new Person("Robert","Male", "Single"));
$persons[] = (new Person("John","Male", "Married"));
$persons[] = (new Person("Laura","Female", "Married"));
$persons[] = (new Person("Diana","Female", "Single"));
$persons[] = (new Person("Mike","Male", "Single"));
$persons[] = (new Person("Bobby","Male", "Single"));

$male = new CriteriaMale();
$female = new CriteriaFemale();
$single = new CriteriaSingle();
$singleMale = new AndCriteria($single, $male);
$singleOrFemale = new OrCriteria($single, $female);

//Males:
//Robert John Mike Bobby
echo "Males:";
$maleList = $male->meetCriteria($persons);
foreach ($maleList as $male){
    echo $male->name.'  ';
}
echo '<br/>';

//Females:
//Laura Diana
echo "Females:";
$maleList = $female->meetCriteria($persons);
foreach ($maleList as $male){
    echo $male->name.'  ';
}
echo '<br/>';


//Single Males:
//Robert Mike Bobby
echo "Single Males:";
$singleMaleList = $singleMale->meetCriteria($persons);
foreach ($singleMaleList as $male){
    echo $male->name.'  ';
}
echo '<br/>';


//Single Or Females:
//Robert Diana Mike Bobby Laura
echo "Single Or Females:";
$singleOrFemaleList = $singleOrFemale->meetCriteria($persons);
foreach ($singleOrFemaleList as $person){
    echo $person->name.'  ';
}
