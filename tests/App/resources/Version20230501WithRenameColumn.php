<?php

namespace App\resources;

class Version20230501WithRenameColumn
{
    public function getDescription(): string
    {
        return 'Foo version';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foo RENAME COLUMN foo_column to bar_column');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
