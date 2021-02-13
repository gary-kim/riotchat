<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2021 Gary Kim <gary@garykim.dev>
 *
 * @author Gary Kim <gary@garykim.dev>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\RiotChat\Dashboard;

use OCA\RiotChat\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\Util;

class RiotChatWidget implements IWidget {

	/** @var IInitialStateService */
	private $initialStateService;
	/** @var IUserSession */
	private $userSession;
	/** @var IL10N */
	private $l10n;

	public function __construct(
		IInitialStateService $initialStateService,
		IUserSession $userSession,
		IL10N $l10n,
		IURLGenerator $urlGenerator
	) {
		$this->initialStateService = $initialStateService;
		$this->userSession = $userSession;
		$this->l10n = $l10n;
	}

	public function getId(): string {
		return 'riotchat';
	}

	public function getTitle(): string {
		return $this->l10n->t('Element for Nextcloud');
	}

	public function getOrder(): int {
		return 50;
	}

	public function getIconClass(): string {
		return 'icon-riotchat-dark';
	}

	public function getUrl(): ?string {
		return null;
	}

	public function load(): void {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return;
		}
		Util::addScript(Application::APP_ID, 'dashboard');
		Util::addStyle(Application::APP_ID, 'dashboard');
	}
}
