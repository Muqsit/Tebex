<?php

declare(strict_types=1);

namespace muqsit\tebex\api\connection;

use RuntimeException;

final class SslConfiguration{

	public static function empty() : SslConfiguration{
		return new self("");
	}

	public static function recommended() : SslConfiguration{
		return self::fromFileName(__DIR__ . "/cacert.pem");
	}

	public static function fromFileName(string $filename) : SslConfiguration{
		$data = file_get_contents($filename);
		assert($data !== false);
		return self::fromData($data);
	}

	public static function fromData(string $data) : SslConfiguration{
		$resource = tmpfile();
		if($resource === false){
			throw new RuntimeException("Failed to create temporary file");
		}

		fwrite($resource, $data);
		return new self(stream_get_meta_data($resource)["uri"], $resource);
	}

	private string $cainfo_path;

	/** @var resource|null */
	private $resource;

	/**
	 * @param string $cainfo_path
	 * @param resource|null $resource
	 */
	public function __construct(string $cainfo_path, $resource = null){
		$this->cainfo_path = $cainfo_path;
		$this->resource = $resource;
	}

	public function getCAInfoPath() : string{
		return $this->cainfo_path;
	}

	public function close() : void{
		if($this->resource !== null){
			fclose($this->resource);
			$this->resource = null;
		}
	}
}