<?php

/*
 * This file is part of the Conveyor package.
 *
 * (c) Jeroen Fiege <jeroen@webcreate.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webcreate\Conveyor\Transporter;

interface TransactionalTransporterInterface
{
    /**
     * @return void
     */
    public function begin();

    /**
     * @return void
     */
    public function commit();
}
