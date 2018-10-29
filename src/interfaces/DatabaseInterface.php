<?php

namespace managers;

use interfaces\EntityInterface;

    /**
     * Interface DatabaseInterface.
     */
    interface DatabaseInterface
    {
        /**
         * @return array|null
         */
        public function getAllRecords():?array;

        /**
         * @param int $id
         */
        public function getRecord(int $id): EntityInterface;

        /**
         * @return mixed
         */
        public function saveRecord(EntityInterface $entity);

        /**
         * @param int $id
         *
         * @return mixed
         */
        public function deleteRecord(int $id): void;

        /**
         * @param int $id
         *
         * @return mixed
         */
        public function updateRecord(int $id, $entity): void;

        /**
         * @param string $filename
         */
        public function importFile(string $filename): void;

        /**
         * @return string
         */
        public function getEntityClass(): string;

        /**
         * @return array
         */
        public function getEntityGetters(): array;

        /**
         * @return array
         */
        public function getEntitySetters(): array;
    }
