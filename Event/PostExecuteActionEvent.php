<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PostExecuteActionEvent extends ActionEvent
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(ActionInterface $action, Response $response)
    {
        parent::__construct($action);

        $this->response = $response;
    }

    /**
     * Returns the response created by the action.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Changes the response returned by the action.
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
