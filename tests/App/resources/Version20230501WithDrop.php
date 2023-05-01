<?php

namespace App\resources;

class Version20230501WithDrop
{
    public function getDescription(): string
    {
        return 'Foo version';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Shippers');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
