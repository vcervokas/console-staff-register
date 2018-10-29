<?php

namespace managers;

use interfaces\EntityInterface;

/**
 * Class ConsoleManager.
 */
class ConsoleManager
{
    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * ConsoleManager constructor.
     *
     * @param DatabaseInterface $db
     */
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Prints help section.
     */
    public function help(): void
    {
        echo '
            Console Staff Management Tool!
            
            Usage: 
                php console.php [command] [[arguments]]
             
            Commands: 
                help
                view
                viewall
                insert 
                update [id]
                delete [id]
                edit [id]
                import [file.csv]
            
            ';
    }

    /**
     * @param array $arguments
     */
    public function view(array $arguments): void
    {
        $id = $arguments[0];
        echo "Record Nr {$id}:";
        echo $this->db->getRecord($id);
        echo "\n";
    }

    /**
     * @param array $arguments
     */
    public function viewall(array $arguments): void
    {
        $allRecords = $this->db->getAllRecords();
        for ($id = 0; $id < count($allRecords); $id++) {
            $this->view([$id]);
        }
    }

    /**
     * @param array $arguments
     */
    public function insert(array $arguments): void
    {
        $entity = $this->collectEntityValues();
        $this->db->saveRecord($entity);
    }

    /**
     * @param array $arguments
     */
    public function update(array $arguments): void
    {
        $id = $arguments[0];
        $this->view($arguments);
        $entity = $this->collectEntityValues();
        $this->db->updateRecord($id, $entity);
    }

    /**
     * @param array $arguments
     */
    public function delete(array $arguments): void
    {
        $id = $arguments[0];
        $this->db->deleteRecord($id);
    }

    /**
     * @param array $arguments
     */
    public function import(array $arguments): void
    {
        $file = $arguments[0];
        $this->db->importFile($file);
    }

    /**
     * @return null|string
     */
    private function readConsoleInput(): ?string
    {
        $line = trim(fgets(STDIN));

        return $line;
    }

    /**
     * @return EntityInterface
     */
    private function collectEntityValues(): EntityInterface
    {
        $entityClass = $this->db->getEntityClass();
        $entity = new $entityClass();
        $setters = $this->db->getEntitySetters();

        foreach ($setters as $setter) {
            echo "{$setter}: ";
            $entity->$setter($this->readConsoleInput());
        }

        return $entity;
    }
}
