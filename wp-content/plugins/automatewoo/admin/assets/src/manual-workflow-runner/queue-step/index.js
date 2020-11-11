/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Card } from '@woocommerce/components';
import { Dashicon } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { PropTypes } from 'prop-types';
import { omit } from 'lodash';

/**
 * Internal dependencies
 */
import { ProgressBar, Button } from '../../base/components';
import { MANUAL_WORKFLOWS_BATCH_SIZE, ADMIN_URL } from '../../settings';
import { addItemBatchToQueue } from '../api-utils';
import NextButton from '../next-button';
import {
	useWarnBeforeUnloadWhileRequesting,
	STEP_STATUSES as STATUSES,
} from '../utils';
import { useQueueItemsReducer } from './data';
import { handleFetchError } from '../../base/utils';
import recordTracksEvent from '../../base/usage-tracking';
import LargeTextAndIcon from '../large-text-and-icon';

const QueueStep = ( {
	items,
	workflow,
	workflowQuickFilterData,
	onStepCancel,
} ) => {
	const [ queueItemsState, queueItemsDispatch ] = useQueueItemsReducer(
		items
	);
	useWarnBeforeUnloadWhileRequesting( queueItemsState.status );
	const itemCount = Object.keys( items ).length;

	useEffect( () => {
		// Only do another request when status is pending
		if ( queueItemsState.status !== STATUSES.PENDING ) {
			return;
		}

		const addItemsBatch = async () => {
			queueItemsDispatch( { type: 'ADD_ITEMS_REQUEST' } );

			// Extract a batch of items from the remaining items
			const batch = Object.keys( queueItemsState.itemsRemaining ).splice(
				0,
				MANUAL_WORKFLOWS_BATCH_SIZE
			);
			const newItemsRemaining = omit(
				queueItemsState.itemsRemaining,
				batch
			);

			try {
				await addItemBatchToQueue( workflow.id, batch );
				queueItemsDispatch( {
					type: 'ADD_ITEMS_SUCCESS',
					itemsRemaining: newItemsRemaining,
				} );
			} catch ( error ) {
				queueItemsDispatch( { type: 'ADD_ITEMS_ERROR' } );
				handleFetchError( 'Error adding items to queue.', error );
			}
		};

		addItemsBatch();
	}, [
		queueItemsState.itemsRemaining,
		queueItemsState.status,
		workflow.id,
		queueItemsDispatch,
	] );

	useEffect(
		() => {
			// Only record tracks event when complete.
			if ( queueItemsState.status !== STATUSES.COMPLETE ) {
				return;
			}

			recordTracksEvent( 'manual_run_workflow_complete', {
				items_count: itemCount,
				conversion_tracking_enabled: workflow.is_conversion_tracking_enabled,
				tracking_enabled: workflow.is_tracking_enabled,
				title: workflow.title,
				type: workflow.type,
				trigger_name: workflow.trigger.name,
			} );
		},
		[ queueItemsState.status, itemCount ]
	);

	const getCardBody = () => {
		if ( queueItemsState.status === STATUSES.COMPLETE ) {
			const successText = sprintf(
				// translators: %(itemCount)d: number of matching items, %(dataType)s: type of item
				__(
					'Woo! %(itemCount)d %(dataType)s were successfully added to the queue.',
					'automatewoo'
				),
				{
					dataType: workflowQuickFilterData.primaryDataTypePluralName,
					itemCount,
				}
			);

			return (
				<>
					<LargeTextAndIcon
						icon={ <Dashicon icon="yes-alt" size="60" /> }
						text={ <p>{ successText }</p> }
					/>
					<div className="automatewoo-workflow-runner-buttons">
						<NextButton
							isPrimary
							isLarge
							href={ `${ ADMIN_URL }admin.php?page=automatewoo-queue&_workflow=${ workflow.id }` }
						>
							{ __( 'View in queue', 'automatewoo' ) }
						</NextButton>
					</div>
				</>
			);
		}

		return (
			<>
				<p>
					{ sprintf(
						// translators: %(itemCount)d: number of matching items, %(dataType)s: type of item, %(workflow)s: workflow title
						__(
							'Adding %(itemCount)d matching %(dataType)s to the queue for the "%(workflow)s" workflow. ' +
								'If you leave this page the process will stop.',
							'automatewoo'
						),
						{
							workflow: workflow.title,
							dataType:
								workflowQuickFilterData.primaryDataTypePluralName,
							itemCount,
						}
					) }
				</p>
				<ProgressBar progress={ queueItemsState.progress } />
				<div className="automatewoo-workflow-runner-buttons">
					<Button isLarge isDefault onClick={ onStepCancel }>
						{ __( 'Cancel', 'automatewoo' ) }
					</Button>
				</div>
			</>
		);
	};

	return (
		<Card title={ __( '3. Add to workflow queue', 'automatewoo' ) }>
			{ getCardBody() }
		</Card>
	);
};

QueueStep.propTypes = {
	workflow: PropTypes.shape( {
		id: PropTypes.number.isRequired,
		title: PropTypes.string.isRequired,
	} ).isRequired,
	workflowQuickFilterData: PropTypes.shape( {
		primaryDataTypePluralName: PropTypes.string.isRequired,
	} ).isRequired,
	items: PropTypes.object.isRequired,
	onStepCancel: PropTypes.func.isRequired,
};

export default QueueStep;
