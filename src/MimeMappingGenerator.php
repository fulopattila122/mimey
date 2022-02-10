<?php

namespace Mimey;

/**
 * Generates a mapping for use in the MimeTypes class.
 *
 * Reads text in the format of httpd's mime.types and generates a PHP array containing the mappings.
 *
 * @psalm-type MimeTypeMap = array{mimes: array<non-empty-string, list<non-empty-string>>, extensions: array<non-empty-string, list<non-empty-string>>}
 */
class MimeMappingGenerator
{
	protected string $mime_types_text;

	/**
	 * Create a new generator instance with the given mime.types text.
	 *
	 * @param non-empty-string $mime_types_text The text from the mime.types file.
	 */
	public function __construct(string $mime_types_text)
	{
		$this->mime_types_text = $mime_types_text;
	}

	/**
	 * Read the given mime.types text and return a mapping compatible with the MimeTypes class.
	 *
	 * @return MimeTypeMap The mapping.
	 */
	public function generateMapping(): array
	{
		$mapping = [];
		$lines = explode("\n", $this->mime_types_text);
		foreach ($lines as $line) {
			$line = trim(preg_replace('~\\#.*~', '', $line));
			$parts = $line ? array_values(array_filter(explode("\t", $line))) : [];
			if (count($parts) === 2) {
				$mime = trim($parts[0]);
				$extensions = explode(' ', $parts[1]);
				foreach ($extensions as $extension) {
					$extension = trim($extension);
					if ($mime && $extension) {
						$mapping['mimes'][$extension][] = $mime;
						$mapping['extensions'][$mime][] = $extension;
						$mapping['mimes'][$extension] = array_unique($mapping['mimes'][$extension]);
						$mapping['extensions'][$mime] = array_unique($mapping['extensions'][$mime]);
					}
				}
			}
		}
		return $mapping;
	}
}
