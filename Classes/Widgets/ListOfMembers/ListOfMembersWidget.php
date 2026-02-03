<?php

/***
 *
 * This file is part of Qc Widgets project.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 <techno@quebec.ca>
 *
 ***/

namespace Qc\QcWidgets\Widgets\ListOfMembers;

use Psr\Http\Message\ServerRequestInterface;
use Qc\QcWidgets\Widgets\AdditionalCssImp;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class ListOfMembersWidget extends AdditionalCssImp implements WidgetInterface, RequestAwareWidgetInterface
{
    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    public function __construct(
        protected WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        protected Provider $dataProvider
    ){}

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Render widget view
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request);
        $data = $this->dataProvider->getItems();
        $view->assignMultiple([
            'data' => $data
        ]);
        return $view->render('Widget/ListOfMembersWidget');
    }

    public function getOptions(): array
    {
        return [];
    }
}
