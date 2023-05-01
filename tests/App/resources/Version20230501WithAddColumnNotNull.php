<?php

class Version20230501WithAddColumnNotNull
{
    public function getDescription(): string
    {
        return 'Dummy version';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dummy ADD dummy_column VARCHAR(255) NOT NULL');
    }

    public function down(): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
