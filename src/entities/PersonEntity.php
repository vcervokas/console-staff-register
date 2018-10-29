<?php

    namespace entities;

    use exceptions\ValidationException;
    use interfaces\EntityInterface;

    /**
     * Entity to manipulate person's data
     *
     * Class PersonEntity
     * @package entities
     */
    class PersonEntity implements EntityInterface
    {

        /**
         * @var string
         */
        private $firstName;

        /**
         * @var string
         */
        private $lastName;

        /**
         * @var string
         */
        private $email;

        /**
         * @var string
         */
        private $phoneNumber1;

        /**
         * @var string
         */
        private $phoneNumber2;

        /**
         * @var string
         */
        private $comment;

        /**
         * @return string
         */
        public function __toString(): string
        {
            return "
            First Name: {$this->firstName}
            Last Name: {$this->lastName}
            Email: {$this->email}
            Phone 1: {$this->phoneNumber1}
            Phone 2: {$this->phoneNumber2}
            Comment: {$this->comment}
            ";
        }

        /**
         * @return string
         */
        public function getFirstName(): string
        {
            return $this->firstName;
        }

        /**
         * @param string $firstName
         * @return PersonEntity
         */
        public function setFirstName(string $firstName): PersonEntity
        {
            if (empty($firstName)) {
                throw new ValidationException('First name must be provided');
            }

            $this->firstName = $firstName;

            return $this;
        }

        /**
         * @return string
         */
        public function getLastName(): string
        {
            return $this->lastName;
        }

        /**
         * @param string $lastName
         * @return PersonEntity
         */
        public function setLastName(string $lastName): PersonEntity
        {
            if (empty($lastName)) {
                throw new ValidationException('Last name must be provided');
            }

            $this->lastName = $lastName;

            return $this;
        }

        /**
         * @return string
         */
        public function getEmail(): string
        {
            return $this->email;
        }

        /**
         * @param string $email
         * @return PersonEntity
         */
        public function setEmail(string $email): PersonEntity
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('Valid email must be provided');
            }
            $this->email = $email;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getPhoneNumber1(): ?string
        {
            return $this->phoneNumber1;
        }

        /**
         * @param string $phoneNumber1
         * @return PersonEntity
         */
        public function setPhoneNumber1(string $phoneNumber1): PersonEntity
        {
            $this->phoneNumber1 = $phoneNumber1;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getPhoneNumber2(): ?string
        {
            return $this->phoneNumber2;
        }

        /**
         * @param string $phoneNumber2
         * @return PersonEntity
         */
        public function setPhoneNumber2(string $phoneNumber2): PersonEntity
        {
            $this->phoneNumber2 = $phoneNumber2;

            return $this;
        }

        /**
         * @return string
         */
        public function getComment(): string
        {
            return $this->comment;
        }

        /**
         * @param string $comment
         * @return PersonEntity
         */
        public function setComment(string $comment): PersonEntity
        {
            $this->comment = $comment;

            return $this;
        }

    }