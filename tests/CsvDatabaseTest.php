<?php
    declare(strict_types=1);

    use entities\PersonEntity;
    use interfaces\EntityInterface;
    use managers\CsvDatabaseManager;
    use managers\DatabaseInterface;
    use PHPUnit\Framework\TestCase;

    class CsvDatabaseTest extends TestCase
    {
        /**
         * @var string
         */
        private $fileName;

        /**
         * @var DatabaseInterface
         */
        private $db;

        /**
         * @var EntityInterface
         */
        private $entity;

        public function setUp()
        {
            $this->fileName = 'test.csv';
            $this->entity = new PersonEntity();
            $this->entity
                ->setFirstName('Testas')
                ->setLastName('Testauskas')
                ->setEmail('testas@testauskas.lt')
                ->setComment('Just Testing');
            $this->db = new CsvDatabaseManager($this->fileName, 'entities\PersonEntity');
            $this->db->saveRecord($this->entity);
        }

        public function tearDown()
        {
            unlink($this->fileName);
        }

        public function test_getEntityClass()
        {
            $this->assertEquals('entities\PersonEntity', $this->db->getEntityClass());
        }

        public function test_getRecordWithWrongId()
        {
            $this->expectException(\exceptions\DatabaseException::class);
            $this->db->getRecord(count($this->db->getAllRecords()) + 999);
        }

        public function test_getRecordWithGoodId()
        {
            $entity = $this->db->getRecord(0);

            $this->assertEquals($entity, $this->entity);
        }

        public function test_saveRecordWithDuplicate()
        {
            $this->expectException(\exceptions\DatabaseException::class);

            $entity = clone $this->entity;
            $this->db->saveRecord($entity);
        }

        public function test_saveRecordWithNewRecord()
        {
            $recordsBefore = count($this->db->getAllRecords());

            $entity = new PersonEntity();
            $entity
                ->setFirstName('NewName')
                ->setLastName('NewSurname')
                ->setEmail('testas@testauskas.lt')
                ->setComment('Just Testing Again!');

            $this->db->saveRecord($entity);
            $recordsAfter = count($this->db->getAllRecords());

            $this->assertEquals($recordsAfter, ($recordsBefore + 1));
        }
    }
