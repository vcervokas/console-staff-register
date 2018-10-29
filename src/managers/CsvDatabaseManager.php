<?php

    namespace managers;

    use exceptions\DatabaseException;
    use interfaces\EntityInterface;

    /**
     * Class CsvDatabaseManager
     * @package managers
     */
    class CsvDatabaseManager implements DatabaseInterface
    {
        /**
         * CSV file delemiter
         */
        const CSV_DELIMITER = ';';

        /**
         * New line delimiter
         */
        const NEW_LINE_DELIMITER = "\r\n";

        /**
         * @var string
         */
        private $dbFile;
        /**
         * @var string
         */
        private $class;

        /**
         * CsvDatabaseManager constructor.
         * @param string $filePath
         * @param string $entityClass
         */
        public function __construct(string $filePath, string $entityClass)
        {
            $this->dbFile = $filePath;
            $this->class = $entityClass;
        }

        /**
         * @return string
         */
        public function getEntityClass(): string
        {
            return $this->class;
        }

        /**
         * @return array|null
         * @throws DatabaseException
         */
        public function getAllRecords(): ?array
        {
            $allRecords = $this->readFile();
            return $allRecords;
        }

        /**
         * @param int $id
         * @return EntityInterface
         * @throws DatabaseException
         */
        public function getRecord(int $id): EntityInterface
        {
            try {
                $entities = $this->readFile();
            } catch (DatabaseException $e) {
                //TODO: log exception and continue working
            }
            if (!array_key_exists($id, $entities)) {
                throw new DatabaseException(sprintf('Record with id %d does not exist.', $id));
            }

            return $entities[$id];
        }

        /**
         * @param EntityInterface $entity
         * @throws DatabaseException
         */
        public function saveRecord(EntityInterface $entity): void
        {
            $entities = [];
            try {
                $entities = $this->readFile();
            } catch (DatabaseException $e) {
                //TODO: log exception and continue working
            }

            $this->checkDuplacates($entities, $entity);

            $entities[] = $entity;
            $this->saveFile($entities);
        }

        /**
         * @param int $id
         * @throws DatabaseException
         */
        public function deleteRecord(int $id): void
        {
            $entities = $this->readFile();
            if (empty($entities[$id])) {
                throw new DatabaseException(sprintf('DB entry with id %d does not exist', $id));
            }
            unset($entities[$id]);
            $this->saveFile($entities);
        }

        /**
         * @param int $id
         * @param $entity
         * @throws DatabaseException
         */
        public function updateRecord(int $id, $entity): void
        {
            $entities = $this->readFile();
            $entities[$id] = $entity;
            $this->saveFile($entities);
        }

        /**
         * @return array
         * @throws DatabaseException
         */
        private function readFile(): array
        {
            $content = null;
            if (file_exists($this->dbFile)) {
                $content = file_get_contents($this->dbFile);
            }
            if(empty($content)) {
                throw new DatabaseException('DB is empty');
            }
            $lines = explode(self::NEW_LINE_DELIMITER, $content);
            $entities = [];
            foreach ($lines as $line) {
                $entities[] = $this->mapToEntity($line);
            }

            return $entities;
        }

        /**
         * @param string $filename
         * @throws DatabaseException
         */
        public function importFile(string $filename): void
        {
            if (strtolower(substr($filename, -3)) != 'csv') {
                throw new DatabaseException('Only csv files are supported');
            }
            if (!file_exists($filename)) {
                throw new DatabaseException(sprintf('Import failed. File %s does not exit', $filename));
            }

            $contents = file_get_contents($filename);
            $lines = explode(self::NEW_LINE_DELIMITER, $contents);
            $entities = [];

            foreach ($lines as $line) {
                $entities[] = $this->mapToEntity($line);
            }

            foreach ($entities as $entity) {
                $this->saveRecord($entity);
            }
        }

        /**
         * @param array $entities
         * @throws DatabaseException
         */
        private function saveFile(array $entities): void
        {
            $contentLine = [];
            foreach ($entities as $entity) {
                $contentLine[] = $this->mapEntityToLine($entity);
            }

            $content = implode(self::NEW_LINE_DELIMITER, $contentLine);
            file_put_contents($this->dbFile, $content);
        }

        /**
         * @param string $csvLine
         * @return EntityInterface
         * @throws DatabaseException
         */
        public function mapToEntity(string $csvLine): EntityInterface
        {
            $csvElements = explode(self::CSV_DELIMITER, $csvLine);
            $elementsRequired = $this->countGetters();
            $elementsProvided = count($csvElements);
            if ($elementsRequired !== $elementsProvided) {
                throw new DatabaseException(
                    sprintf(
                        'Wrong number of elements provided. Provided %s. Should be %s',
                        $elementsProvided, $elementsRequired
                    )
                );
            }

            $entity = new $this->class;
            $elementNumber = 0;

            foreach ($this->getEntitySetters() as $method) {
                $entity->$method($csvElements[$elementNumber]);
                $elementNumber++;
            }

            return $entity;
        }

        /**
         * @param $entity
         * @return string
         * @throws DatabaseException
         */
        public function mapEntityToLine($entity): string
        {
            $providedEntityClass = get_class($entity);
            $requiredEntityClass = $this->class;

            if ($providedEntityClass !== $requiredEntityClass) {
                throw new DatabaseException(
                    sprintf(
                        'Wrong entity provided. Provided entity is %s. Should be %s',
                        $providedEntityClass, $requiredEntityClass
                    )
                );
            }

            $values = [];

            foreach ($this->getEntityGetters() as $method) {
                $values[] = $entity->$method();
            }

            return implode(self::CSV_DELIMITER, $values);
        }

        /**
         * @return array
         */
        public function getEntityGetters(): array
        {
            $getters = [];
            foreach (get_class_methods($this->class) as $method) {
                if ($this->isGetterMethod($method)) {
                    $getters[] = $method;
                }
            }

            return $getters;
        }

        /**
         * @return array
         */
        public function getEntitySetters(): array
        {
            $setters = [];
            foreach (get_class_methods($this->class) as $method) {
                if ($this->isSetterMethod($method)) {
                    $setters[] = $method;
                }
            }

            return $setters;
        }

        /**
         * @param string $methodName
         * @return bool
         */
        private function isGetterMethod(string $methodName): bool
        {
            return substr( $methodName, 0, 3 ) === "get";
        }

        /**
         * @param string $methodName
         * @return bool
         */
        private function isSetterMethod(string $methodName): bool
        {
            return substr( $methodName, 0, 3 ) === "set";
        }

        /**
         * @return int
         */
        private function countGetters():int
        {
            $count = 0;
            foreach (get_class_methods($this->class) as $method) {
                if ($this->isGetterMethod($method)) {
                    $count++;
                }
            }

            return $count;
        }

        /**
         * @param array $entities
         * @param EntityInterface $entity
         * @throws DatabaseException
         */
        private function checkDuplacates(array $entities, EntityInterface $entity): void
        {
            foreach ($entities as $e) {
                if ((string)$e === (string)$entity) {
                    throw new DatabaseException("Entity already exists");
                }
            }
        }
    }