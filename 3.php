<?php

class FileSeekable implements \SeekableIterator
{
    private $file;
    private $position;

    function __construct($fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception ('File not found');
        }
        $this->file = @fopen($fileName, 'r');
        $this->position = 0;

        if (!$this->file) {
            throw new \Exception ('Broken file');
        }
    }

    public function next()
    {
        fgets($this->file);
        $this->position++;
    }

    public function valid()
    {
        return !feof($this->file);
    }

    public function current()
    {
        $current_position = ftell($this->file);
        $string = fgets($this->file);
        //return position back
        fseek($this->file, $current_position);

        return $string;
    }

    public function rewind()
    {
        $this->position = 0;
        rewind($this->file);
    }

    public function seek($position)
    {
        $this->position = $position;
        for ($i = 0; $i < $position; $i++) {
            $this->next();
        }
        if (!$this->valid()) {
            throw new \Exception('Position not founded');
        }

        return $this->current();
    }

    public function key()
    {
        return $this->position;
    }
}