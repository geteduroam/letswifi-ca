<?php declare(strict_types=1);

/*
 * This file is part of letswifi; a system for easy eduroam device enrollment
 *
 * Copyright: 2018-2021, Jørn Åne de Jong, Uninett AS <jorn.dejong@uninett.no>
 * Copyright: 2020-2021, Paul Dekkers, SURF <paul.dekkers@surf.nl>
 * SPDX-License-Identifier: BSD-3-Clause
 */

namespace letswifi\browserauth;

use RuntimeException;

class MismatchIdpException extends RuntimeException
{
	/** @var string */
	private $required;

	/** @var ?string */
	private $provided;

	/** @var ?string $ */
	private $username;

	public function __construct( string $required, ?string $provided, string $username = null )
	{
		$this->required = $required;
		$this->provided = $provided;
		$this->username = $username;
		parent::__construct(
			"Expected IdP \"${required}\" but got " .
				(
					null === $provided
						? 'nothing'
						: "\"${provided}\""
				) . (
					isset( $username )
						?
						" with username \"${username}\""
						: ''
				)
			);
	}
}
