<?php

/**
 * @file
 * Contains a QueryImplementationInterface
 */

namespace Vultan\Query;

/**
 * Interface QueryImplementationInterface
 *
 * @package Vultan\BaseQuery
 */
interface QueryImplementationInterface {

  /**
   * Do any necessary query setup.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function setup();

  /**
   * Pre-execution step.
   */
  public function preExecute();

  /**
   * Execute the prepared query.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  public function execute();


  /**
   * Post-execution step.
   */
  public function postExecute();

  /**
   * Post process some results.
   *
   * @param mixed $results
   *   The original results.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  public function postProcess($results);
}
