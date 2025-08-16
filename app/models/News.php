

<?php
/**
 * News Class
 * Represents a single news record from the database.
 */
class News
{
    public ?int $id;
    public ?string $title;
    public ?string $body;
    public ?string $image_url;
    public ?string $published_at;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $body = null,
        ?string $image_url = null,
        ?string $published_at = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->image_url = $image_url;
        $this->published_at = $published_at;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
